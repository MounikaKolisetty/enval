import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TrainingVmf2Component } from './training-vmf2.component';

describe('TrainingVmf2Component', () => {
  let component: TrainingVmf2Component;
  let fixture: ComponentFixture<TrainingVmf2Component>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TrainingVmf2Component]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(TrainingVmf2Component);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
